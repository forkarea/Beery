<?php

declare(strict_types=1);

namespace Tests\Behat\Context\Application;

use App\Application\Command\RateBeer;
use App\Domain\Beer\Event\BeerRated;
use App\Domain\Beer\Model\Beer;
use App\Domain\Beer\Model\Connoisseur as BeerConnoisseur;
use App\Domain\Beer\Model\Rate;
use App\Domain\Connoisseur\Model\Connoisseur;
use Behat\Behat\Context\Context;
use Prooph\ServiceBus\CommandBus;
use Tests\Service\Prooph\Plugin\CollectedMessage;
use Tests\Service\Prooph\Plugin\EventsRecorder;
use Webmozart\Assert\Assert;

final class RateContext implements Context
{
    /** @var CommandBus */
    private $commandBus;

    /** @var EventsRecorder */
    private $eventsRecorder;

    public function __construct(CommandBus $commandBus, EventsRecorder $eventsRecorder)
    {
        $this->commandBus = $commandBus;
        $this->eventsRecorder = $eventsRecorder;
    }

    /**
     * @When /^(I) rate the ("[^"]+" beer) (\d+)$/
     */
    public function iRateTheBeer(Connoisseur $connoisseur, Beer $beer, float $rate)
    {
        $email = $connoisseur->email();

        $this->commandBus->dispatch(RateBeer::create(
            new BeerConnoisseur($email->value()),
            $beer->id(),
            new Rate($rate)
        ));
    }

    /**
     * @Then the :beer beer should have average rate :rate
     */
    public function theBeerShouldHaveAverageRate(Beer $beer, float $expectedRate)
    {
        $messages = $this->eventsRecorder->getAllMessages();
        $beerRates = [];

        /** @var CollectedMessage $message */
        foreach ($messages as $message) {
            $domainEvent = $message->event();

            if ($domainEvent instanceof BeerRated && $domainEvent->beerId() == $beer->id()) {
                $rate = $domainEvent->rate();

                $beerRates[] = $rate->value();
            }
        }

        Assert::same(array_sum($beerRates) / count($beerRates), $expectedRate);
    }
}

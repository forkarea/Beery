<?php

declare(strict_types=1);

namespace spec\App\Infrastructure\ReadModel\Projection;

use App\Domain\Beer\Event\BeerAdded;
use App\Domain\Beer\Event\BeerRated;
use App\Domain\Beer\Model\Abv;
use App\Domain\Beer\Model\Connoisseur;
use App\Domain\Beer\Model\Id;
use App\Domain\Beer\Model\Name;
use App\Domain\Beer\Model\Rate;
use App\Infrastructure\ReadModel\Projection\BeerProjection;
use App\Infrastructure\ReadModel\View\BeerView;
use App\Infrastructure\Repository\BeerViews;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class BeerProjectionSpec extends ObjectBehavior
{
    function let(BeerViews $beerViews)
    {
        $this->beConstructedWith($beerViews);
    }

    function it_is_a_beer_projection(): void
    {
        $this->shouldHaveType(BeerProjection::class);
    }

    function it_creates_a_beer_view(BeerViews $beerViews)
    {
        $beerViews->add(Argument::exact(new BeerView(
            'e8a68535-3e17-468f-acc3-8a3e0fa04a59',
            'King of Hop',
            5
        )))->shouldBeCalled();

        $this(BeerAdded::withData(
            new Id('e8a68535-3e17-468f-acc3-8a3e0fa04a59'),
            new Name('King of Hop'),
            new Abv(5))
        );
    }

    function it_updates_a_beer_view_rate(BeerViews $beerViews, BeerView $beerView)
    {
        $beerViews->get(Argument::exact(new Id('e8a68535-3e17-468f-acc3-8a3e0fa04a59')))->willReturn($beerView);

        $beerView->rate(5)->shouldBeCalled();
        $beerViews->save()->shouldBeCalled();

        $this(BeerRated::withData(
            new Id('e8a68535-3e17-468f-acc3-8a3e0fa04a59'),
            new Connoisseur('rick@morty.com'),
            new Rate(5))
        );
    }
}

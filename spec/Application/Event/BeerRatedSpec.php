<?php

declare(strict_types=1);

namespace spec\App\Application\Event;

use App\Domain\Model\Id;
use App\Domain\Model\Rate;
use PhpSpec\ObjectBehavior;
use Prooph\Common\Messaging\DomainEvent;

final class BeerRatedSpec extends ObjectBehavior
{
    function it_represents_beer_added_event_occurrence()
    {
        $this->beConstructedThrough('occur', [
            new Id('e8a68535-3e17-468f-acc3-8a3e0fa04a59'),
            new Id('02b9ccbc-e30b-4ec2-8fb9-339609f36c65'),
            new Rate(5),
        ]);

        $this->connoisseurId()->shouldBeLike(new Id('e8a68535-3e17-468f-acc3-8a3e0fa04a59'));
        $this->beerId()->shouldBeLike(new Id('02b9ccbc-e30b-4ec2-8fb9-339609f36c65'));
        $this->rate()->shouldBeLike(new Rate(5));
    }

    function it_is_a_domain_event(): void
    {
        $this->shouldHaveType(DomainEvent::class);
    }
}
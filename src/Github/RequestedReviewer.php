<?php

namespace Dgame\GitBot\Github;

/**
 * Class RequestedReviewer
 * @package Dgame\GitBot\Github
 */
final class RequestedReviewer
{
    /**
     * @var Review[]
     */
    private $reviews = [];
    /**
     * @var Reviewer[]
     */
    private $reviewer = [];

    /**
     * RequestedReviewer constructor.
     *
     * @param PullRequest $request
     */
    public function __construct(PullRequest $request)
    {
        foreach ($request->getReviews() as $review) {
            $this->reviews[$review->getReviewer()->getName()] = $review;
        }
        $this->reviewer = $request->getReviewer();
    }

    /**
     * @param PullRequest $request
     *
     * @return RequestedReviewer
     */
    public static function of(PullRequest $request): self
    {
        return new self($request);
    }

    /**
     * @return bool
     */
    public function haveAllApproved(): bool
    {
        foreach ($this->reviewer as $reviewer) {
            $name = $reviewer->getName();
            if (!array_key_exists($name, $this->reviews)) {
                return false;
            }

            $review = $this->reviews[$name];
            if (!$review->isApproved()) {
                return false;
            }
        }

        return true;
    }
}
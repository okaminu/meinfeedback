<?php
namespace MFB\FeedbackBundle\Entity;

class FeedbackSummaryPage
{
    protected $currentPageNumber;

    protected $lastPageNumber;

    protected $feedbackSummaryItemList = array();

    /**
     * @param $currentPageNumber
     * @param $lastPageNumber
     */
    public function __construct($currentPageNumber, $lastPageNumber)
    {
        $this->currentPageNumber = $currentPageNumber;
        $this->lastPageNumber = $lastPageNumber;
    }

    /**
     * @param FeedbackSummaryItem $item
     */
    public function addItem(FeedbackSummaryItem $item)
    {
        $this->feedbackSummaryItemList[] = $item;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->feedbackSummaryItemList;
    }

    /**
     * @return mixed
     */
    public function getCurrentPageNumber()
    {
        return $this->currentPageNumber;
    }

    /**
     * @return mixed
     */
    public function getLastPageNumber()
    {
        return $this->lastPageNumber;
    }

    public function getItemsCount()
    {
        return count($this->feedbackSummaryItemList);
    }

}

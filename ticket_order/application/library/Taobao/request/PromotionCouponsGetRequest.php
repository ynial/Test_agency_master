<?php
/**
 * TOP API: taobao.promotion.coupons.get request
 * 
 * @author auto create
 * @since 1.0, 2014-12-17 15:38:42
 */
class PromotionCouponsGetRequest
{
	/** 
	 * 优惠券的id，唯一标识这个优惠券
	 **/
	private $couponId;
	
	/** 
	 * 优惠券的面额，必须是3，5，10，20，50,100<br /> 支持最大值为：100<br /> 支持最小值为：3
	 **/
	private $denominations;
	
	/** 
	 * 优惠券的截止日期
	 **/
	private $endTime;
	
	/** 
	 * 查询的页号，结果集是分页返回的，每页20条<br /> 支持最小值为：1
	 **/
	private $pageNo;
	
	/** 
	 * 每页条数<br /> 支持的最大列表长度为：20
	 **/
	private $pageSize;
	
	private $apiParas = array();
	
	public function setCouponId($couponId)
	{
		$this->couponId = $couponId;
		$this->apiParas["coupon_id"] = $couponId;
	}

	public function getCouponId()
	{
		return $this->couponId;
	}

	public function setDenominations($denominations)
	{
		$this->denominations = $denominations;
		$this->apiParas["denominations"] = $denominations;
	}

	public function getDenominations()
	{
		return $this->denominations;
	}

	public function setEndTime($endTime)
	{
		$this->endTime = $endTime;
		$this->apiParas["end_time"] = $endTime;
	}

	public function getEndTime()
	{
		return $this->endTime;
	}

	public function setPageNo($pageNo)
	{
		$this->pageNo = $pageNo;
		$this->apiParas["page_no"] = $pageNo;
	}

	public function getPageNo()
	{
		return $this->pageNo;
	}

	public function setPageSize($pageSize)
	{
		$this->pageSize = $pageSize;
		$this->apiParas["page_size"] = $pageSize;
	}

	public function getPageSize()
	{
		return $this->pageSize;
	}

	public function getApiMethodName()
	{
		return "taobao.promotion.coupons.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkMaxValue($this->denominations,100,"denominations");
		RequestCheckUtil::checkMinValue($this->denominations,3,"denominations");
		RequestCheckUtil::checkMinValue($this->pageNo,1,"pageNo");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}

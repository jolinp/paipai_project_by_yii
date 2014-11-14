<?php

/**
 * 增强版 DateTime
 *
 * 提供以下功能
 * 1.获取当前日期第一天和最后一天
 *
 * @author Jeffrey Au <fly88oj@163.com>
 * @version $Id$
 */
class ExtendedDateTime extends DateTime
{
    /**
     * 输出当前日期所在月份的第一天
     *
     * @return string
     */
    public function formatToFirstDateOfMonth()
    {
        $timestamp = $this->format('U');
        return date('Y-m-01', $timestamp);
    }

    /**
     * 输出当前日期所在月份的最后一天
     *
     * @return string
     */
    public function formatToLastDateOfMonth()
    {
        $timestamp = $this->format('U');
        return date('Y-m-t', $timestamp);
    }
}
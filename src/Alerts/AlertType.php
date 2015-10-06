<?php
namespace Alerts;

/**
 * Defines the built in types used by the Alerts class to classify each Message
 * @package Alerts
 */
class AlertType
{
    const SUCCESS = 'success';
    const NOTICE = 'notice';
    const ERROR = 'error';
    const WARNING = 'warning';
}
<?php

namespace dmstr\bootstrap;

use yii\web\View;

/**
 * @inheritdoc
 */
class Tabs extends \yii\bootstrap\Tabs
{
    /**
     * Register assetBundle
     */
    public static function registerAssets()
    {
        // Register route cookie function script
        \Yii::$app->controller->getView()->registerJs(
            '
            function setRouteCookie(route, controllerId)
            {
                jQuery.cookie.raw = true;
                jQuery.cookie("_bs_route_" + controllerId, route, { path: "/" });
            }',
            View::POS_HEAD,
            'setRouteCookie'
        );

        BootstrapAsset::register(\Yii::$app->controller->getView());
    }

    /**
     * @param $route
     * @param $controllerId
     */
    public static function rememberActiveTab($route, $controllerId)
    {
        self::registerAssets();

        // Register cookie script
        \Yii::$app->controller->getView()->registerJs(
            '
            var route        = "' . $route . '";
            var controllerId = "' . $controllerId . '";

            jQuery("#relation-tabs > li > a").on("click", function (event) {
                var activeTab     = jQuery(this).attr("href");
                jQuery.cookie.raw = true;
                jQuery.cookie("_bs_activeTab_" + controllerId, activeTab, { path: "/" });
            });

            jQuery(window).on("load", function () {
                var activeTab = jQuery.cookie("_bs_activeTab_" + controllerId);
                if (activeTab !== "") {
                    jQuery("[href=" + activeTab + "]").tab("show");
                }
            });',
            View::POS_READY,
            'rememberActiveTab'
        );
    }

    /**
     * @param $controllerId
     *
     * @return \yii\web\Cookie
     */
    public static function getParentRelationRoute($controllerId)
    {
        \Yii::$app->request->enableCookieValidation = false;

        if (isset(\Yii::$app->request->cookies["_bs_route_" . $controllerId])) {
            return \Yii::$app->request->cookies["_bs_route_" . $controllerId]->value;
        } else {
            return null;
        }
    }

    /**
     * @param $controllerId
     *
     * @return null|string
     */
    public static function getParentRelationActiveTab($controllerId)
    {
        \Yii::$app->request->enableCookieValidation = false;

        if (isset(\Yii::$app->request->cookies["_bs_activeTab_" . $controllerId])) {
            return \Yii::$app->request->cookies["_bs_activeTab_" . $controllerId]->value;
        } else {
            return null;
        }
    }

    public static function clearParentRelationRoute($controllerId)
    {
        self::registerAssets();

        // Register delete cookie script
        \Yii::$app->controller->getView()->registerJs(
            '
            var controllerId  = "' . $controllerId . '";
            jQuery.cookie.raw = true;
            jQuery.removeCookie("_bs_activeTab_" + controllerId, { path: "/" });
            jQuery.removeCookie("_bs_route_" + controllerId, { path: "/" });
            ',
            View::POS_READY,
            'clearParentRelationRoute'
        );
    }

}
<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;
?>

<style type="text/css">
    .the7-spinner {
        width: 72px;
        height: 72px;
        position: relative;
    }
    .the7-spinner > div {
        border-radius: 50%;
        width: 9px;
        left: 0;
        box-sizing: border-box;
        display: block;
        position: absolute;
        border: 9px solid #fff;
        width: 72px;
        height: 72px;
    }
    .the7-spinner-ring-bg{
        opacity: 0.25;
    }
    div.the7-spinner-ring {
        animation: spinner-animation 0.8s cubic-bezier(1, 1, 1, 1) infinite;
        border-color:var(--the7-beautiful-spinner-color2) transparent transparent transparent;
    }

    @keyframes spinner-animation{
        from{
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
</style>

<div class="the7-spinner">
    <div class="the7-spinner-ring-bg"></div>
    <div class="the7-spinner-ring"></div>
</div>
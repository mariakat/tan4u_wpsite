<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;
?>
<style type="text/css">
    .the7-spinner {
        width: 60px;
        height: 72px;
        position: relative;
    }
    .the7-spinner > div {
        animation: spinner-animation 1.2s cubic-bezier(1, 1, 1, 1) infinite;
        width: 9px;
        left: 0;
        display: inline-block;
        position: absolute;
        background-color:var(--the7-beautiful-spinner-color2);
        height: 18px;
        top: 27px;
    }
    div.the7-spinner-animate-2 {
        animation-delay: 0.2s;
        left: 13px
    }
    div.the7-spinner-animate-3 {
        animation-delay: 0.4s;
        left: 26px
    }
    div.the7-spinner-animate-4 {
        animation-delay: 0.6s;
        left: 39px
    }
    div.the7-spinner-animate-5 {
        animation-delay: 0.8s;
        left: 52px
    }
    @keyframes spinner-animation {
        0% {
            top: 27px;
            height: 18px;
        }
        20% {
            top: 9px;
            height: 54px;
        }
        50% {
            top: 27px;
            height: 18px;
        }
        100% {
            top: 27px;
            height: 18px;
        }
    }
</style>

<div class="the7-spinner">
    <div class="the7-spinner-animate-1"></div>
    <div class="the7-spinner-animate-2"></div>
    <div class="the7-spinner-animate-3"></div>
    <div class="the7-spinner-animate-4"></div>
    <div class="the7-spinner-animate-5"></div>
</div>
<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;
?>
<style type="text/css">
    [class*="the7-spinner-animate-"]{
        animation: spinner-animation 1s cubic-bezier(1,1,1,1) infinite;
        x:46.5px;
        y:40px;
        width:7px;
        height:20px;
        fill:var(--the7-beautiful-spinner-color2);
        opacity: 0.2;
    }
    .the7-spinner-animate-2{
        animation-delay: 0.083s;
    }
    .the7-spinner-animate-3{
        animation-delay: 0.166s;
    }
    .the7-spinner-animate-4{
         animation-delay: 0.25s;
    }
    .the7-spinner-animate-5{
         animation-delay: 0.33s;
    }
    .the7-spinner-animate-6{
         animation-delay: 0.416s;
    }
    .the7-spinner-animate-7{
         animation-delay: 0.5s;
    }
    .the7-spinner-animate-8{
         animation-delay: 0.58s;
    }
    .the7-spinner-animate-9{
         animation-delay: 0.666s;
    }
    .the7-spinner-animate-10{
         animation-delay: 0.75s;
    }
    .the7-spinner-animate-11{
        animation-delay: 0.83s;
    }
    .the7-spinner-animate-12{
        animation-delay: 0.916s;
    }
    @keyframes spinner-animation{
        from {
            opacity: 1;
        }
        to{
            opacity: 0;
        }
    }
</style>
<svg width="75px" height="75px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
	<rect class="the7-spinner-animate-1" rx="5" ry="5" transform="rotate(0 50 50) translate(0 -30)"></rect>
	<rect class="the7-spinner-animate-2" rx="5" ry="5" transform="rotate(30 50 50) translate(0 -30)"></rect>
	<rect class="the7-spinner-animate-3" rx="5" ry="5" transform="rotate(60 50 50) translate(0 -30)"></rect>
	<rect class="the7-spinner-animate-4" rx="5" ry="5" transform="rotate(90 50 50) translate(0 -30)"></rect>
	<rect class="the7-spinner-animate-5" rx="5" ry="5" transform="rotate(120 50 50) translate(0 -30)"></rect>
	<rect class="the7-spinner-animate-6" rx="5" ry="5" transform="rotate(150 50 50) translate(0 -30)"></rect>
	<rect class="the7-spinner-animate-7" rx="5" ry="5" transform="rotate(180 50 50) translate(0 -30)"></rect>
	<rect class="the7-spinner-animate-8" rx="5" ry="5" transform="rotate(210 50 50) translate(0 -30)"></rect>
	<rect class="the7-spinner-animate-9" rx="5" ry="5" transform="rotate(240 50 50) translate(0 -30)"></rect>
	<rect class="the7-spinner-animate-10" rx="5" ry="5" transform="rotate(270 50 50) translate(0 -30)"></rect>
	<rect class="the7-spinner-animate-11" rx="5" ry="5" transform="rotate(300 50 50) translate(0 -30)"></rect>
	<rect class="the7-spinner-animate-12" rx="5" ry="5" transform="rotate(330 50 50) translate(0 -30)"></rect>
</svg>
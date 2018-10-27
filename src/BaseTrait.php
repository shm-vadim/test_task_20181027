<?php

namespace App;

trait BaseTrait
{
    private function dt($dt)
    {
        return \DT::createFromDT($dt);
    }

    private function dts($seconds)
    {
        return \DT::createFromTimestamp($seconds);
    }
}

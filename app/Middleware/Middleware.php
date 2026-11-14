<?php

namespace Atlas\Middleware;

interface Middleware
{
    public function handle(): void;
}
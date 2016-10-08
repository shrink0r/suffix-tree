<?php

namespace Shrink0r\SuffixTree\Builder;

interface BuilderInterface
{
    /**
     * @param  string $S
     *
     * @return mixed
     */
    public function build(string $S);
}

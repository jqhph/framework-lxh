<?php

function gen() {
    $ret = (yield 'yield1');
    var_dump('fun '. $ret);
    $ret = (yield 'yield2');
    var_dump($ret);
}

$gen = gen();
var_dump(($gen->key()));    // string(6) "yield1"
var_dump('out '.$gen->send('ret1')); // string(4) "ret1"   (the first var_dump in gen)
// string(6) "yield2" (the var_dump of the ->send() return value)
var_dump($gen->send('ret2')); // string(4) "ret2"   (again from within gen)
// NULL               (the return value of ->send())


function stackedCoroutine(Generator $gen) {
    $stack = new SplStack;

    for (;;) {
        $value = $gen->current();

        if ($value instanceof Generator) {
            $stack->push($gen);
            $gen = $value;
            continue;
        }

        $isReturnValue = $value instanceof CoroutineReturnValue;
        if (!$gen->valid() || $isReturnValue) {
            if ($stack->isEmpty()) {
                return;
            }

            $gen = $stack->pop();
            $gen->send($isReturnValue ? $value->getValue() : NULL);
            continue;
        }

        $gen->send(yield $gen->key() => $value);
    }
}
<?php

declare(strict_types=1);

namespace Mollie\Laravel\Tests\TempHelpers;

use Illuminate\Support\Str;

trait LaravelTrait
{
    /**
     * Assert that a class can be automatically injected.
     *
     * @param string $name
     *
     * @return void
     */
    public function assertIsInjectable(string $name)
    {
        $injectable = true;

        $message = "The class '$name' couldn't be automatically injected.";

        try {
            $class = $this->makeInjectableClass($name);
            $this->assertInstanceOf($name, $class->getInjectedObject());
        } catch (Exception $e) {
            $injectable = false;
            if ($msg = $e->getMessage()) {
                $message .= " $msg";
            }
        }

        $this->assertTrue($injectable, $message);
    }

    /**
     * Register and make a stub class to inject into.
     *
     * @param string $name
     *
     * @return object
     */
    protected function makeInjectableClass(string $name)
    {
        do {
            $class = 'testBenchStub'.Str::random();
        } while (class_exists($class));

        eval("
            class $class
            {
                protected \$object;

                public function __construct(\\$name \$object)
                {
                    \$this->object = \$object;
                }

                public function getInjectedObject()
                {
                    return \$this->object;
                }
            }
        ");

        return $this->app->make($class);
    }
}
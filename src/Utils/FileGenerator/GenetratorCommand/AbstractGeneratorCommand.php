<?

namespace Bitrock\Utils\FileGenerator\GenetratorCommand;

use Bitrock\Utils\FileGenerator\Generator\AbstractGenerator;
use Bitrock\Utils\FileGenerator\Prototypes\AbstractPrototype;
use Bitrock\Utils\FileGenerator\Stubs\AbstractStub;

abstract class AbstractGeneratorCommand
{
    /** @property AbstractPrototype $prototype */
    protected $prototype;

    /** @property AbstractStub $prototype */
    protected $stub;

    /** @property AbstractGenerator $prototype */
    protected $generator;

    public function __construct(
        AbstractStub $stub,
        AbstractPrototype $prototype,
        string $generatorClassName
    )
    {
        $this->prototype = $prototype;
        $this->stub = $stub;
        $this->generator = new $generatorClassName($prototype, $stub);
    }

    abstract public function execute($params);
}
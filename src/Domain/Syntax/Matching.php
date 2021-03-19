<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

class Matching implements SyntaxInterface
{
    private string $field;

    /** @var mixed */
    private $value;

    /** @var mixed */
    private $fuzziness;

    public function __construct(string $field, $value = null, $fuzziness = 'auto')
    {
        $this->field = $field;
        $this->value = $value;
        $this->fuzziness = $fuzziness;
    }

    public function build(): array
    {
        $query = [ 'query' => $this->value ];

        if (!empty($this->fuzziness)) {
            $query['fuzziness'] = $this->fuzziness;
        }

        return ['match' => [ $this->field => $query ] ];
    }
}

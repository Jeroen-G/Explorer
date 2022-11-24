<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

class MultiMatch implements SyntaxInterface
{
    private mixed $value;

    private ?array $fields;

    private mixed $fuzziness;

    public function __construct(string $value, ?array $fields = null, $fuzziness = 'auto')
    {
        $this->value = $value;
        $this->fields = $fields;
        $this->fuzziness = $fuzziness;
    }

    public function build(): array
    {
        $query = ['query' => $this->value ];

        if ($this->fields !== null) {
            $query['fields'] = $this->fields;
        }

        if (!empty($this->fuzziness)) {
            $query['fuzziness'] = $this->fuzziness;
        }

        return [ 'multi_match' => $query ];
    }
}

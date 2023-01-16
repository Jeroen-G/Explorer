<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

class MultiMatch implements SyntaxInterface
{
    private mixed $value;

    private ?array $fields;

    private mixed $fuzziness;

    private $prefix_length;

    public function __construct(string $value, ?array $fields = null, $fuzziness = 'auto', $prefix_length = 0)
    {
        $this->value = $value;
        $this->fields = $fields;
        $this->fuzziness = $fuzziness;
        $this->prefix_length = $prefix_length;
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

        if (!empty($this->prefix_length)) {
            $query['prefix_length'] = $this->prefix_length;
        }

        return [ 'multi_match' => $query ];
    }
}

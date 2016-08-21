<?php

namespace PHPCD\ClassInfo;

class ClassFilter
{
    const IS_ABSTRACT_CLASS   = 'isAbstractClass';
    const IS_FINAL            = 'isFinal';
    const IS_TRAIT            = 'isTrait';
    const IS_INSTANTIABLE     = 'isInstantiable';
    const IS_INTERFACE        = 'isInterface';

    private $criteria = [];

    private $criteriaNames = [
        self::IS_ABSTRACT_CLASS,
        self::IS_FINAL,
        self::IS_TRAIT,
        self::IS_INSTANTIABLE,
        self::IS_INTERFACE
    ];

    public function __construct(array $criteria)
    {
        foreach ($this->criteriaNames as $field) {
            if (isset($criteria[$field])) {
                $this->validateField($criteria[$field]);
                $this->criteria[$field] = $criteria[$field];
            } else {
                $this->criteria[$field] = null;
            }
        }

        // @todo Disable it to normal usage
        $this->validate();
    }

    public function getCriteriaNames()
    {
        return $this->criteriaNames;
    }

    private function validateField($field)
    {
        if ($field !== true && $field !== false && $field !== null) {
            $message = sprintf('%s must be set to true, false or null.', (string)$field);
            throw new \InvalidArgumentException($message);
        }

        return true;
    }

    /**
     * Probably usable only as remainder
     * develop calling requests the client
     */
    private function validate()
    {
        $wrongCombinations = [
            [self::IS_ABSTRACT_CLASS => true, self::IS_TRAIT => true],
            [self::IS_ABSTRACT_CLASS => true, self::IS_INTERFACE => true],
            [self::IS_ABSTRACT_CLASS => true, self::IS_FINAL => true],
            [self::IS_ABSTRACT_CLASS => true, self::IS_INSTANTIABLE => true],
            [self::IS_INSTANTIABLE => true, self::IS_TRAIT => true],
            [self::IS_INSTANTIABLE => true, self::IS_INTERFACE => true],
            [self::IS_TRAIT => true, self::IS_INTERFACE => true],
            [self::IS_TRAIT => true, self::IS_FINAL => true],
            [self::IS_INTERFACE => true, self::IS_FINAL => true],
            [self::IS_INSTANTIABLE => false, self::IS_FINAL => true]
        ];

        foreach ($wrongCombinations as $combination) {
            $criteriaNames = [];
            foreach ($combination as $field => $value) {
                if ($this->criteria[$field] === $value) {
                    $criteriaNames[] = $field;
                }
            }

            if (count($criteriaNames) === count($combination)) {
                $message = sprintf(
                    'Bad search criteria: [%s] used at once.',
                    implode(',', $criteriaNames)
                );
                throw new \InvalidArgumentException($message);
            }
        }

        return true;
    }

    /**
     * @return bool|null
     */
    public function isAbstractClass()
    {
        return $this->criteria[self::IS_ABSTRACT_CLASS];
    }

    /**
     * @return bool|null
     */
    public function isFinal()
    {
        return $this->criteria[self::IS_FINAL];
    }

    /**
     * @return bool|null
     */
    public function isTrait()
    {
        return $this->criteria[self::IS_TRAIT];
    }

    /**
     * @return bool|null
     */
    public function isInstantiable()
    {
        return $this->criteria[self::IS_INSTANTIABLE];
    }

    /**
     * @return bool|null
     */
    public function isInterface()
    {
        return $this->criteria[self::IS_INTERFACE];
    }
}

<?php

namespace App\Actions\Products;

class CompareAction
{
    private const MAX = 3;

    private const SESSION_KEY = 'compare_ids';

    public function add(string $id): void
    {
        $ids = $this->all();

        if (in_array($id, $ids, true)) {
            return;
        }

        if ($this->isFull()) {
            return;
        }

        $ids[] = $id;

        session()->put(self::SESSION_KEY, $ids);
    }

    public function remove(string $id): void
    {
        $ids = $this->all();

        $ids = array_values(
            array_filter($ids, fn (string $i): bool => $i !== $id)
        );

        session()->put(self::SESSION_KEY, $ids);
    }

    public function all(): array
    {
        return session()->get(self::SESSION_KEY, []);
    }

    public function count(): int
    {
        return count($this->all());
    }

    public function isFull(): bool
    {
        return $this->count() >= self::MAX;
    }

    public function max(): int
    {
        return self::MAX;
    }

    public function has(string $id): bool
    {
        return in_array($id, $this->all(), true);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }
}

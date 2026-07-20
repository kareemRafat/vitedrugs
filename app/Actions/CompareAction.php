<?php

namespace App\Actions;

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

        if (count($ids) >= self::MAX) {
            return;
        }

        $ids[] = $id;

        session([self::SESSION_KEY => $ids]);
    }

    public function remove(string $id): void
    {
        $ids = $this->all();

        $ids = array_values(array_filter(
            $ids,
            fn (string $v): bool => $v !== $id
        ));

        session([self::SESSION_KEY => $ids]);
    }

    public function all(): array
    {
        return session(self::SESSION_KEY, []);
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

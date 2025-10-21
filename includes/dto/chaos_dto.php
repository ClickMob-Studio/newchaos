<?php

final class ChaosUser implements JsonSerializable
{
    public function __construct(
        public int $userId,
        public int $soulsCurrent,
        public int $soulsCollected,
        public int $soulsSpent,
        public ?int $lanternEquipped,
        public int $curseLevel,
        public int $curseExp,
        public ?string $updatedAt,
    ) {
    }

    public static function fromRow(array $r): self
    {
        return new self(
            (int) $r['user_id'],
            (int) $r['souls_current'],
            (int) $r['souls_collected'],
            (int) $r['souls_spent'],
            isset($r['lantern_equipped']) ? (int) $r['lantern_equipped'] : null,
            (int) $r['curse_level'],
            (int) $r['curse_exp'],
            $r['updated_at'] ?? null
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'user_id' => $this->userId,
            'souls_current' => $this->soulsCurrent,
            'souls_collected' => $this->soulsCollected,
            'souls_spent' => $this->soulsSpent,
            'lantern_equipped' => $this->lanternEquipped,
            'curse_level' => $this->curseLevel,
            'curse_exp' => $this->curseExp,
            'updated_at' => $this->updatedAt,
        ];
    }
}

final class ChaosPassEntry implements JsonSerializable
{
    public function __construct(
        public int $id,
        public bool $isPremium,
        public int $curseLevel,
        public string $rewardType,
        public ?int $rewardRefId,
        public int $rewardQty
    ) {
    }

    public static function fromRow(array $r): self
    {
        return new self(
            (int) $r['id'],
            (int) $r['is_premium'] === 1,
            (int) $r['curse_level'],
            (string) $r['reward_type'],
            isset($r['reward_ref_id']) ? (int) $r['reward_ref_id'] : null,
            (int) $r['reward_qty']
        );
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}

final class ChaosPassState implements JsonSerializable
{
    /** @param int[] $claimableIds */
    public function __construct(
        public int $curseLevel,
        public int $curseExp,
        public bool $isPremium,
        public ?int $nextReqExp,
        public int $progressPct,
        public bool $atMaxLevel,
        public array $claimableIds
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'curse_level' => $this->curseLevel,
            'curse_exp' => $this->curseExp,
            'is_premium' => $this->isPremium,
            'next_req_exp' => $this->nextReqExp,  // null if max level
            'progress_pct' => $this->progressPct, // 0..100
            'at_max_level' => $this->atMaxLevel,
            'claimable_ids' => $this->claimableIds,
        ];
    }
}
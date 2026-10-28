<?php

namespace Database\Factories;

use App\Enums\InquiryTopic;
use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true).' team',
            'description' => fake()->sentence(),
            'topics' => [],
            'auto_assign' => false,
            'is_active' => true,
        ];
    }

    public function handling(InquiryTopic ...$topics): static
    {
        return $this->state(['topics' => array_map(fn (InquiryTopic $topic): string => $topic->value, $topics)]);
    }

    public function autoAssign(): static
    {
        return $this->state(['auto_assign' => true]);
    }

    /**
     * @param  array<int, User>  $users
     */
    public function withMembers(array $users, TeamRole $role = TeamRole::Member): static
    {
        return $this->afterCreating(function (Team $team) use ($users, $role): void {
            foreach ($users as $user) {
                $team->members()->attach($user, ['role' => $role->value]);
            }
        });
    }
}

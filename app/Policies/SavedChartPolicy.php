<?php

// app/Policies/SavedChartPolicy.php
namespace App\Policies;

use App\Models\SavedChart;
use App\Models\User;

class SavedChartPolicy
{
    public function viewAny(User $user)
    {
        return true; // Any authenticated user can view their charts
    }

    public function view(User $user, SavedChart $savedChart)
    {
        return $user->id === $savedChart->user_id; // User can only view their own charts
    }

    public function create(User $user)
    {
        return true; // Any authenticated user can create a chart
    }

    public function delete(User $user, SavedChart $savedChart)
    {
        return $user->id === $savedChart->user_id; // User can only delete their own charts
    }
}

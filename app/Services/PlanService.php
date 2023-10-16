<?php

namespace App\Services;

use App\Repositories\PlanRepository;
use App\Repositories\UserRepository;

use Illuminate\Http\Request;

class PlanService
{

    protected $planRepository;
    protected $userRepository;

    public function __construct(PlanRepository $planRepository, UserRepository $userRepository)
    {

        $this->planRepository = $planRepository;
        $this->userRepository = $userRepository;
    }
}
<?php

namespace App\Enums;

enum ConferenceRoleType: string
{
    case Admin    = 'admin';      // Conference Chair / Admin
    case PcMember = 'pc_member'; // Program Committee Member
    case Reviewer = 'reviewer';  // External Reviewer
}

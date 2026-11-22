<?php

namespace App\Http\Requests;

use App\Models\Admin;
use App\Models\MissingReport;

class AdminUpdateMissingReportRequest extends UpdateMissingReportRequest
{
    public function authorize(): bool
    {
        $report = $this->route('report');

        return $report instanceof MissingReport
            && $this->user('admin') instanceof Admin
            && $report->status === MissingReport::STATUS_PENDING;
    }
}

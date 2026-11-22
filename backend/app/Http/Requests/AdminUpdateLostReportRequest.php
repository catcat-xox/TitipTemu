<?php

namespace App\Http\Requests;

use App\Models\Admin;
use App\Models\LostReport;

class AdminUpdateLostReportRequest extends UpdateLostReportRequest
{
    public function authorize(): bool
    {
        $report = $this->route('report');

        return $report instanceof LostReport
            && $this->user('admin') instanceof Admin
            && $report->status === 'pending';
    }
}

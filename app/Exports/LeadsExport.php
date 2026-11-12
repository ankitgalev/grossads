<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LeadsExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected $leads;
    protected $fieldNames;

    public function __construct($leads, $fieldNames)
    {
        $this->leads = $leads;
        $this->fieldNames = $fieldNames;
    }

    public function headings(): array
    {
        return array_merge(
            [
                'ID',
                'Campaign',
                'Publisher',
                'Advertiser'
            ],
            $this->fieldNames->toArray(),
            [
                'Status',
                'Submitted'
            ]
        );
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->leads as $lead) {

            $fieldValues = [];

            foreach ($this->fieldNames as $fieldName) {

                $matchedValue = $lead->values->first(function ($value) use ($fieldName) {
                    return optional($value->field)->field_name === $fieldName;
                });

                $fieldValues[] = $matchedValue
                    ? $matchedValue->field_value
                    : '';
            }

            $rows[] = array_merge(
                [
                    $lead->id,
                    optional($lead->campaign)->name ?? 'N/A',
                    optional($lead->publisher)->name
                        ?? optional($lead->publisher)->email
                        ?? 'N/A',
                    optional($lead->advertiser)->company_name ?? 'N/A'
                ],
                $fieldValues,
                [
                    $lead->status,
                    $lead->created_at
                        ? $lead->created_at->format('d-m-Y H:i:s')
                        : 'N/A'
                ]
            );
        }

        return $rows;
    }
}

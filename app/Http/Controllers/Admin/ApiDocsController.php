<?php
//namespace App\Http\Controllers\Admin;
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\CampaignPublisher;

class ApiDocsController extends Controller
{
    public function index()
    {
        $assignments = CampaignPublisher::with([
            'campaign.fields',
            'publisher'
        ])->orderBy('id', 'desc')->get();

        foreach ($assignments as $assignment) {

            $sampleData = array();

            if ($assignment->campaign) {
                foreach ($assignment->campaign->fields as $field) {

                    switch ($field->field_type) {
                        case 'email':
                            $sampleData[$field->field_key] =
                                'publisher@example.com';
                            break;

                        case 'phone':
                            $sampleData[$field->field_key] =
                                '9876543210';
                            break;

                        case 'number':
                            $sampleData[$field->field_key] = 100;
                            break;

                        case 'date':
                            $sampleData[$field->field_key] =
                                '2026-10-09';
                            break;

                        case 'select':
                            $options = $field->options;

                            if (is_string($options)) {
                                $decoded = json_decode($options, true);

                                if (json_last_error() === JSON_ERROR_NONE) {
                                    $options = $decoded;
                                } else {
                                    $options = explode(',', $options);
                                }
                            }

                            if (is_array($options) && count($options)) {
                                $sampleData[$field->field_key] =
                                    reset($options);
                            } else {
                                $sampleData[$field->field_key] =
                                    'Example option';
                            }
                            break;

                        case 'textarea':
                            $sampleData[$field->field_key] =
                                'Example message';
                            break;

                        default:
                            $sampleData[$field->field_key] =
                                'Example text';
                            break;
                    }
                }
            }

            $sampleBody = array(
                'data' => $sampleData
            );

            $jsonBody = json_encode(
                $sampleBody,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            );

            $assignment->sample_json = $jsonBody;

            $apiUrl = url('/api/v1/leads');
            $apiKey = $assignment->api_key;

            $assignment->curl_example =
                "curl --location '" . $apiUrl . "' \\\n" .
                "--header 'Accept: application/json' \\\n" .
                "--header 'Content-Type: application/json' \\\n" .
                "--header 'X-API-KEY: " . $apiKey . "' \\\n" .
                "--data-raw '" .
                str_replace("'", "'\\''", $jsonBody) .
                "'";

            $assignment->php_example =
                "<?php\n\n" .
                "\$url = '" . $apiUrl . "';\n" .
                "\$apiKey = '" . $apiKey . "';\n\n" .
                "\$payload = " .
                var_export($sampleBody, true) . ";\n\n" .
                "\$ch = curl_init(\$url);\n" .
                "curl_setopt(\$ch, CURLOPT_POST, true);\n" .
                "curl_setopt(\$ch, CURLOPT_RETURNTRANSFER, true);\n" .
                "curl_setopt(\$ch, CURLOPT_HTTPHEADER, array(\n" .
                "    'Accept: application/json',\n" .
                "    'Content-Type: application/json',\n" .
                "    'X-API-KEY: ' . \$apiKey\n" .
                "));\n" .
                "curl_setopt(\$ch, CURLOPT_POSTFIELDS, json_encode(\$payload));\n\n" .
                "\$response = curl_exec(\$ch);\n" .
                "\$httpCode = curl_getinfo(\$ch, CURLINFO_HTTP_CODE);\n\n" .
                "if (\$response === false) {\n" .
                "    echo curl_error(\$ch);\n" .
                "} else {\n" .
                "    echo \$response;\n" .
                "}\n\n" .
                "curl_close(\$ch);\n";
        }

        return view('admin.api-docs.index', compact('assignments'));
    }
}

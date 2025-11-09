<?php
/**
 * HubSpot API Integration
 *
 * Handles communication with HubSpot CRM API.
 *
 * @package Travel\Forms\Integrations
 * @since 1.0.0
 */

namespace Travel\Forms\Integrations;

class HubSpotAPI
{
    /**
     * HubSpot API base URL.
     */
    private const API_BASE_URL = 'https://api.hubapi.com';

    /**
     * API access token.
     */
    private string $access_token;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->access_token = get_option('travel_forms_hubspot_api_key', '');
    }

    /**
     * Check if HubSpot is properly configured.
     *
     * @return bool
     */
    public function is_configured(): bool
    {
        return !empty($this->access_token);
    }

    /**
     * Create or update a contact in HubSpot.
     *
     * @param array  $form_data Form submission data
     * @param string $form_type Form type identifier
     *
     * @return array|false HubSpot response or false on failure
     */
    public function create_contact(array $form_data, string $form_type)
    {
        if (!$this->is_configured()) {
            error_log('HubSpot API: Not configured');
            return false;
        }

        $email = $form_data['email'] ?? '';
        if (empty($email)) {
            error_log('HubSpot API: No email provided');
            return false;
        }

        // Map form fields to HubSpot properties
        $properties = $this->map_form_data_to_properties($form_data, $form_type);

        // Create/update contact
        $endpoint = '/crm/v3/objects/contacts';
        $body = [
            'properties' => $properties,
        ];

        $response = $this->make_request('POST', $endpoint, $body);

        if ($response && isset($response['id'])) {
            // Log successful creation
            error_log('HubSpot API: Contact created/updated - ID: ' . $response['id']);
            return $response;
        }

        return false;
    }

    /**
     * Map form data to HubSpot contact properties.
     *
     * @param array  $form_data Form submission data
     * @param string $form_type Form type
     *
     * @return array HubSpot properties
     */
    private function map_form_data_to_properties(array $form_data, string $form_type): array
    {
        $properties = [
            'email' => $form_data['email'] ?? '',
        ];

        // Standard HubSpot properties
        if (!empty($form_data['name'])) {
            // Try to split first/last name
            $name_parts = explode(' ', trim($form_data['name']), 2);
            $properties['firstname'] = $name_parts[0] ?? '';
            $properties['lastname'] = $name_parts[1] ?? '';
        }

        if (!empty($form_data['phone'])) {
            $properties['phone'] = $form_data['phone'];
        }

        if (!empty($form_data['country'])) {
            $properties['country'] = $form_data['country'];
        }

        // Custom properties based on form type
        switch ($form_type) {
            case 'contact-form':
                if (!empty($form_data['subject'])) {
                    $properties['contact_subject'] = $form_data['subject'];
                }
                if (!empty($form_data['message'])) {
                    $properties['contact_message'] = $form_data['message'];
                }
                $properties['lifecyclestage'] = 'lead';
                break;

            case 'booking-form':
                if (!empty($form_data['tour_name'])) {
                    $properties['tour_interest'] = $form_data['tour_name'];
                }
                if (!empty($form_data['travel_date'])) {
                    $properties['preferred_travel_date'] = $form_data['travel_date'];
                }
                if (!empty($form_data['num_travelers'])) {
                    $properties['number_of_travelers'] = $form_data['num_travelers'];
                }
                if (!empty($form_data['special_requests'])) {
                    $properties['special_requests'] = $form_data['special_requests'];
                }
                $properties['lifecyclestage'] = 'opportunity';
                break;

            case 'brochure-form':
                if (!empty($form_data['brochure_type'])) {
                    $properties['brochure_interest'] = $form_data['brochure_type'];
                }
                if (!empty($form_data['travel_timeline'])) {
                    $properties['travel_timeline'] = $form_data['travel_timeline'];
                }
                if (!empty($form_data['newsletter']) && $form_data['newsletter']) {
                    $properties['newsletter_subscription'] = 'true';
                }
                $properties['lifecyclestage'] = 'subscriber';
                break;
        }

        // Add form source
        $properties['form_source'] = $form_type;
        $properties['hs_lead_status'] = 'NEW';

        return $properties;
    }

    /**
     * Make an API request to HubSpot.
     *
     * @param string $method   HTTP method
     * @param string $endpoint API endpoint
     * @param array  $body     Request body
     *
     * @return array|false Response data or false on failure
     */
    private function make_request(string $method, string $endpoint, array $body = [])
    {
        $url = self::API_BASE_URL . $endpoint;

        $args = [
            'method' => $method,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->access_token,
                'Content-Type' => 'application/json',
            ],
            'timeout' => 30,
        ];

        if (!empty($body)) {
            $args['body'] = wp_json_encode($body);
        }

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            error_log('HubSpot API Error: ' . $response->get_error_message());
            return false;
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);

        // HubSpot returns 200 or 201 for success
        if ($status_code >= 200 && $status_code < 300) {
            return $data;
        }

        // Log error
        error_log('HubSpot API Error: Status ' . $status_code . ' - ' . $response_body);

        return false;
    }

    /**
     * Search for a contact by email.
     *
     * @param string $email Email address
     *
     * @return array|false Contact data or false if not found
     */
    public function search_contact_by_email(string $email)
    {
        if (!$this->is_configured()) {
            return false;
        }

        $endpoint = '/crm/v3/objects/contacts/search';
        $body = [
            'filterGroups' => [
                [
                    'filters' => [
                        [
                            'propertyName' => 'email',
                            'operator' => 'EQ',
                            'value' => $email,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->make_request('POST', $endpoint, $body);

        if ($response && isset($response['results']) && !empty($response['results'])) {
            return $response['results'][0];
        }

        return false;
    }

    /**
     * Test API connection.
     *
     * @return bool True if connection is successful
     */
    public function test_connection(): bool
    {
        if (!$this->is_configured()) {
            return false;
        }

        $endpoint = '/crm/v3/objects/contacts?limit=1';
        $response = $this->make_request('GET', $endpoint);

        return $response !== false;
    }
}

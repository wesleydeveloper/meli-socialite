<?php


namespace Kolovious\MeliSocialite\Traits;


use Kolovious\MeliSocialite\Facade\Meli;

trait MeliRequests
{
    /**
     * Get user by id or current user
     * @param string|int|null $userId
     * @return object|null
     * @throws \Exception
     */
    public function getUser($userId = 'me')
    {
        return $this->response(Meli::withAuthToken()->get("users/{$userId}"));
    }

    /**
     * Get all items or get item by id.
     * @param array|string|null $items
     * @param array|null $options
     * @return object|null
     * @throws \Exception
     */
    public function getItems($items = null, $options = null)
    {

        if ($items) {
            if (is_array($items)) {
                $items = implode(',', $items);
            }
            $params = ['ids' => $items];
            if ($options) {
                $params = array_merge($params, $options);
            }
            return $this->response(Meli::withAuthToken()->get('items', $params));
        }
        $user = $this->getUser();
        $params = ['order' => 'last_updated_desc'];
        if ($options) {
            $params = array_merge($params, $options);
        }
        return $this->response(Meli::withAuthToken()->get("users/{$user->id}/items/search", $params));
    }

    /**
     * Get orders all or get order by id
     * @param string|int|null $orderId
     * @param array|null $options
     * @return object|null
     * @throws \Exception
     */
    public function getOrders($orderId = null, $options = null)
    {
        $user = $this->getUser();
        $params = ['seller' => $user->id, 'sort' => 'date_desc'];
        if ($orderId) {
            $params['q'] = $orderId;
        }
        if ($options) {
            $params = array_merge($params, $options);
        }
        return $this->response(Meli::withAuthToken()->get('orders/search', $params));
    }

    /**
     * Get shipments by order id
     * @param string|int $orderId
     * @return object|null
     * @throws \Exception
     */
    public function getShipmentsByOrder($orderId)
    {
        return $this->response(Meli::withAuthToken()->get("orders/{$orderId}/shipments"));
    }

    /**
     * Get shipments by id
     * @param int|string $shipmentId
     * @return object
     * @throws \Exception
     */
    public function getShipment($shipmentId)
    {
        return $this->response(Meli::withAuthToken()->get("shipments/{$shipmentId}"));
    }

    /**
     * Get shipment Label pdf by id
     * @param array|string|int $shipmentIds
     * @param string $responseType
     * @return array
     */
    public function getShipmentLabels($shipmentIds, $responseType = 'pdf')
    {
        $params = [
            'shipment_ids' => is_array($shipmentIds) ? implode(',', $shipmentIds) : $shipmentIds,
            'response_type' => $responseType,
            'caller.id' => $this->getUser()->id
        ];
        return Meli::withAuthToken()->download('shipment_labels', $params);
    }


    /**
     * Send answer
     * @param string|int $questionId
     * @param string $answer
     * @return object|null
     * @throws \Exception
     */
    public function sendAnswer($questionId, $answer)
    {
        $params = ['question_id' => $questionId, 'text' => $answer];
        return $this->response(Meli::withAuthToken()->post('answers', $params));
    }

    /**
     * Get all questions
     * @param array|null $options
     * @return object|null
     * @throws \Exception
     */
    public function getQuestions($options = null)
    {
        return $this->response(Meli::withAuthToken()->get('my/received_questions/search', $options));
    }

    /**
     * Create user test
     * @return object|null
     * @throws \Exception
     */
    public function createUserTest()
    {
        return $this->response(Meli::post('users/test_user', ['site_id' => 'MLB']));
    }

    /**
     * Validate response
     * @param array $request
     * @return object|null
     * @throws \Exception
     */
    private function response(array $request)
    {
        if ($request['httpCode'] === 200) {
            return $request['body'];
        }
        throw new \Exception($request['body'], (int)$request['httpCode']);
    }
}

<?php


namespace Kolovious\MeliSocialite\Traits;


use Kolovious\MeliSocialite\Facade\Meli;

trait MeliRequests
{
    /**
     * Get user by id or current user
     * @param string|int|null $user
     * @return object|null
     */
    public function getUser($user = 'me')
    {
        return $this->response(Meli::withAuthToken()->get("users/{$user}"));
    }

    /**
     * Get all items or get item by id.
     * @param array|string|null $items
     * @return object|null
     */
    public function getItems($items = null)
    {
        if ($items) {
            if (is_array($items)) {
                $items = implode(',', $items);
            }
            return $this->response(Meli::withAuthToken()->get('items', ['ids' => $items]));
        } else {
            $user = $this->getUser();
            return $this->response(Meli::withAuthToken()->get("users/{$user->id}/items/search"));
        }
    }

    /**
     * Get orders all or get order by id
     * @param string|int|null $order
     * @return object|null
     */
    public function getOrders($order = null)
    {
        $user = $this->getUser();
        $params = ['seller' => $user->id];
        if ($order) {
            $params['q'] = $order;
        }
        return $this->response(Meli::withAuthToken()->get('orders/search', $params));
    }

    /**
     * Get shipments by order id
     * @param string|int $order
     * @return object|null
     */
    public function getShipments($order)
    {
        return $this->response(Meli::withAuthToken()->get("orders/{$order}/shipments"));
    }

    /**
     * Get shipment Label pdf by id
     * @param string|int $shipment
     * @return object|null
     */
    public function getShipmentLabels($shipment)
    {
        return $this->response(Meli::withAuthToken()->get('shipment_labels', ['shipment_ids' => $shipment, 'savePdf' => 'Y']));
    }

    /**
     * Get all questions
     * @return object|null
     */
    public function getQuestions()
    {
        return $this->response(Meli::withAuthToken()->get('my/received_questions/search'));
    }

    /**
     * Create user test
     * @return object|null
     */
    public function createUserTest()
    {
        return $this->response(Meli::post('users/test_user', ['site_id' => 'MLB']));
    }

    /**
     * Validate response
     * @param array $request
     * @return object|null
     */
    private function response($request)
    {
        if ($request['httpCode'] === 200) {
            return $request['body'];
        }
        return null;
    }
}

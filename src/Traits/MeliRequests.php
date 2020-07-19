<?php


namespace Kolovious\MeliSocialite\Traits;


use Kolovious\MeliSocialite\Facade\Meli;

trait MeliRequests
{
    /**
     * Get user by id
     * @param string|int|null $user
     * @return object|null
     */
    public function getUser($user = 'me')
    {
        return $this->response(Meli::withAuthToken()->get("users/{$user}"));
    }

    /**
     * Get all items or get item by id.
     * @param array|null $items
     * @return array|null
     */
    public function getItems($items = null)
    {
        if ($items) {
            if (is_array($items)) {
                $items = implode(',', $items);
            }
            return $this->response(Meli::withAuthToken()->get('items', ['ids' => $items]));
        } else {
            $user = $this->currentUser();
            return $this->response(Meli::withAuthToken()->get("users/{$user->id}/items/search"));
        }
    }

    /**
     * Get orders all or get order by id
     * @param string|int|null $order
     * @return array|null
     */
    public function getOrders($order = null)
    {
        $user = $this->currentUser();
        $params = ['seller' => $user->id];
        if ($order) {
            $params['q'] = $order;
        }
        return $this->response(Meli::withAuthToken()->get('orders/search', $params))->results;
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
     * Get all answers
     * @return array|object|null
     */
    public function getAnswers()
    {
        return $this->response(Meli::withAuthToken()->post('answers'));
    }

    /**
     * Get current user
     * @return object|null
     */
    private function currentUser()
    {
        return $this->response(Meli::withAuthToken()->get("users/me"));
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
     * @return object|array|null
     */
    private function response($request)
    {
        if ($request['httpCode'] === 200) {
            return $request['body'];
        }
        return null;
    }
}

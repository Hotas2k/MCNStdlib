<?php
/**
 * Copyright (c) 2011-2013 Antoine Hedgecock.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of the
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author      Antoine Hedgecock <antoine@pmg.se>
 * @author      Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright   2011-2013 Antoine Hedgecock
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 */

namespace MCNStdlib\Controller\Plugin;

use Zend\Http\Header\GenericHeader;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class Http
 * @package MCNStdlib\Controller\Plugin
 */
class Http extends AbstractPlugin
{
    /**
     * Retrieves the sorting field and direction
     *
     * @param string  $field
     * @param string  $direction
     * @param boolean $fromQuery
     *
     * @return array
     */
    public function getSort($field, $direction, $fromQuery = false)
    {
        if ($fromQuery) {

            $sort = trim($this->controller->params()->fromQuery('sort', null));

        } else {

            $sort = trim($this->controller->params('sort', null));
        }

        if ($sort === null || empty($sort)) {

            return array($field, $direction);
        }

        if (substr($sort, 0, 1) == '-') {

            return array(substr($sort, 1), 'DESC');
        }

        return array($sort, 'ASC');
    }

    /**
     * Get the range for the http request
     *
     * @param integer $limit
     * @param integer $offset
     *
     * @return array
     */
    public function getRange($limit = 10, $offset = 0)
    {
        $headers = $this->controller->getRequest()->getHeaders();

        if ($headers->has('Range')) {

            $exp = explode('-', substr($headers->get('Range')->getFieldValue(), 6));

            return array($exp[0], $exp[1] - $exp[0]);
        }

        return array($offset, $limit);
    }

    /**
     * Set the correct response according to the range
     *
     * @param integer $offset
     * @param integer $limit
     * @param integer $total
     *
     * @return void
     */
    public function setRange($offset, $limit, $total)
    {
        // construct it
        $header = new GenericHeader('Content-Range', sprintf('%d-%d/%d', $offset, $limit, $total));

        // append the header
        $this->controller->getResponse()->getHeaders()->addHeader($header);
    }
}

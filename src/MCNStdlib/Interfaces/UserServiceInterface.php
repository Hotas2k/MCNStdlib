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

namespace MCNStdlib\Interfaces;

use Doctrine\Common\Collections\Criteria;
use MCNStdlib\Interfaces\UserEntityInterface;

/**
 * Class UserServiceInterface
 * @package MCNUser\Service
 */
interface UserServiceInterface
{
    /**
     * Get a user by a given property
     *
     * @param string $property
     * @param mixed  $value
     *
     * @return \MCNStdlib\Interfaces\UserEntityInterface|null
     */
    public function getOneBy($property, $value);

    /**
     * Get a user by their ID
     *
     * @param integer $id
     *
     * @return \MCNStdlib\Interfaces\UserEntityInterface|null
     */
    public function getById($id);

    /**
     * Get a user by their email
     *
     * @param string $email
     *
     * @return \MCNStdlib\Interfaces\UserEntityInterface|null
     */
    public function getByEmail($email);

    /**
     * Save the user
     *
     * @param \MCNStdlib\Interfaces\UserEntityInterface $user
     *
     * @return void
     */
    public function save(UserEntityInterface $user);

    /**
     * Remove a user
     *
     * @param \MCNStdlib\Interfaces\UserEntityInterface $user
     *
     * @return void
     */
    public function remove(UserEntityInterface $user);

    /**
     * @param \Doctrine\Common\Collections\Criteria $criteria
     *
     * @return \Zend\Paginator\Paginator
     */
    public function search(Criteria $criteria);

    /**
     * @param \Doctrine\Common\Collections\Criteria $criteria
     *
     * @return \Zend\Paginator\Paginator
     */
    public function fetchAll(Criteria $criteria);
}

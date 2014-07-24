<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Base
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Base\Controller\Plugin;

use Base\Model\User;
use Base\Model\Interest;
use Base\Model\Post;
use Base\Model\Traveloti;
use Config\Model\Profile;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Finds friends
 * @author Blair
 */
class FriendFinder extends AbstractPlugin {

	private static $keywords = array('the', 'and', 'or', 'a', 'an', 'at', 'to');

	public function findFriends() {
		/* @var $member User */
		/* @var $memberProfile Interest */
		/* @var $masterProfile Profile */
		/* @var $userProfile Interest */
		/* @var $user User */

		$em = $this->getController()->getEntityManager();

		// Fetch member
		$member = $this->getController()->zfcUserAuthentication()->getIdentity();
		$memberId = $member->getId();

		// Initialize variables
		$users = array();
		$rank = array();

		/*
		 * Fetch users who meet profile criteria.
		* This method iterates over each profile and performs a separate database
		* query. Ranking is determined by the number of times a user is fetched.
		*/
		$memberProfiles = $member->getInterests()->getValues();
		foreach ($memberProfiles as $memberProfile) {
			$masterProfile = $memberProfile->getProfile();
			$profiles = $masterProfile->getInterests();

			foreach ($profiles as $userProfile) {
				$user = $userProfile->getUser();

				// Test for identity
				if($user == $member){
					continue;
				}

				// Compare user against the member's friends
				if($member->getFollowers()->contains($user)
					|| $member->getFollowing()->contains($user)) {
					continue;
				}

				// Add the user to the array or upgrade the user's rank
				$userId = $user->getId();
				if(!array_key_exists($userId, $users)) {
					$users[$userId] = $user;
					$rank[$userId] = 1;
				} else {
					$rank[$userId]++;
				}
			}
		}

		// Sort users by rank
		if(count($users)) {
			array_multisort($rank, SORT_DESC, $users);
		}

		return $users;
	}

	/**
	 * Returns an array of Traveloti based on the specified Profile. This
	 * method excludes a member's connections.
	 *
	 * @param Profile $profile
	 * @return array
	 */
	public function findTraveloti(Profile $profile) {
		// Fetch member
		$member = $this->getController()->zfcUserAuthentication()->getIdentity();
		$memberId = $member->getId();

		// Initialize variables
		$users = array();
		$rank = array();

		$profiles = $profile->getInterests();
		foreach($profiles as $userProfile) {
			$user = $userProfile->getUser();

			// Test for identity
			if($user == $member){
				continue;
			}

			// Compare user against the member's friends
			if($member->getFollowers()->contains($user)
				|| $member->getFollowing()->contains($user)) {
				continue;
			}

			// Add the user to the array or upgrade the user's rank
			$userId = $user->getId();
			if(!array_key_exists($userId, $users)) {
				$users[$userId] = $user;
				$rank[$userId] = 1;
			} else {
				$rank[$userId]++;
			}
		}

		// Sort users by rank
		if(count($users)) {
			array_multisort($rank, SORT_DESC, $users);
		}

		return $users;
	}

	/**
	 * Returns an array of Traveloti based on the specified word(s)
	 * in the given $queryString. This method will return a member's
	 * connections.
	 *
	 * @param string $queryString
	 * @return array
	 */
	public function search($queryString) {
		/* @var $em EntityManager */
		/* @var $qb QueryBuilder */
		/* @var $traveloti Traveloti */

		$em = $this->getController()->getEntityManager();

		// Fetch member
		$member = $this->getController()->zfcUserAuthentication()->getIdentity();
		$memberId = $member->getId();

		// Split the string into individual words
		$subject = (string) $queryString;
		$pattern = '/\W+/';
		$queries = preg_split($pattern, $subject);

		$result = array();
		$rank = array();
		$numQueries = count($queries);
		foreach($queries as $query) {
			// Exclude keywords
			if((strlen($query) < 3) || (in_array($query, self::$keywords))) {
				continue;
			}
			
			$query = '%' . $query . '%';
				
			$qb = $em->createQueryBuilder();
			$qb->select('t')
			->from('Base\Model\Traveloti', 't')
			->leftJoin('t.statuses', 'st')
			->leftJoin('t.photos', 'ph')
			->leftJoin('t.links', 'lnk')
			->where($qb->expr()->andX(
				$qb->expr()->neq('t.id', $memberId),
				$qb->expr()->orX(
					$qb->expr()->like('st.message', ':query'),
					$qb->expr()->like('st.place', ':query'),
					$qb->expr()->like('ph.name', ':query'),
					$qb->expr()->like('ph.place', ':query'),
					$qb->expr()->like('lnk.description', ':query'),
					$qb->expr()->like('lnk.message', ':query'),
					$qb->expr()->like('lnk.name', ':query')
			)));
			$qb->setParameter('query', $query);
				
			$q = $qb->getQuery();
			$rs = $q->getResult();
			foreach ($rs as $traveloti) {
				// Add the Traveloti to the result array or upgrade its rank
				$id = strval($traveloti->getId());
				if(!array_key_exists($id, $result)) {
					$result[$id] = $traveloti;
					$rank[$id] = 1;
				} else {
					$rank[$id]++;
				}
			}
		}

		// Sort traveloti by rank
		if(count($result)) {
			array_multisort($rank, SORT_DESC, $result);
		}

		return $result;
	}
}
?>
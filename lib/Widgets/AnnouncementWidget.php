<?php declare(strict_types=1);


/**
 * Nextcloud - Announcement Widget for Dashboard
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Maxence Lange <maxence@artificial-owl.com>
 * @copyright 2018, Maxence Lange <maxence@artificial-owl.com>
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\AnnouncementCenter\Widgets;


use OC\L10N\L10N;
use OCA\AnnouncementCenter\AppInfo\Application;
use OCA\AnnouncementCenter\Widgets\Service\AnnouncementService;
use OCP\AppFramework\QueryException;
use OCP\Dashboard\IDashboardWidget;
use OCP\Dashboard\Model\IWidgetRequest;
use OCP\Dashboard\Model\IWidgetSettings;
use OCP\IL10N;
use OCP\L10N\IFactory;

class AnnouncementWidget implements IDashboardWidget {


	const WIDGET_ID = 'announcement-center';

	/** @var IL10N */
	private $l10n;


	/** @var AnnouncementService */
	private $announcementService;


	public function __construct(IFactory $factory) {
		$this->l10n = $factory->get('announcementcenter');
	}


	/**
	 * @return string
	 */
	public function getId(): string {
		return self::WIDGET_ID;
	}


	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->l10n->t('Announcements');
	}


	/**
	 * @return string
	 */
	public function getDescription(): string {
		return $this->l10n->t('Display last announcements');
	}


	/**
	 * @return array
	 */
	public function getTemplate(): array {
		return [
			'app'      => 'announcementcenter',
			'icon'     => 'icon-announcement',
			'css'      => 'widgets/announcement',
			'js'       => 'widgets/announcement',
			'content'  => 'widgets/announcement',
			'function' => 'OCA.DashBoard.announcementCenter.init'
		];
	}


	/**
	 * @return array
	 */
	public function widgetSetup(): array {
		return [
			'size' => [
				'min'     => [
					'width'  => 5,
					'height' => 2
				],
				'default' => [
					'width'  => 6,
					'height' => 3
				]
			],
			'push' => 'OCA.DashBoard.announcementCenter.push'
		];
	}


	/**
	 * @param IWidgetSettings $settings
	 */
	public function loadWidget(IWidgetSettings $settings) {
		$app = new Application();

		$container = $app->getContainer();
		try {
			$this->announcementService = $container->query(AnnouncementService::class);
		} catch (QueryException $e) {
			return;
		}
	}


	/**
	 * @param IWidgetRequest $request
	 */
	public function requestWidget(IWidgetRequest $request) {
		if ($request->getRequest() === 'getLastAnnouncement') {
			$request->addResult(
				'lastAnnouncement', $this->announcementService->getLastAnnouncement()
			);
		}
	}


}
<?php

namespace PetraBarus\Yii2\GooglePlacesAutoComplete;

use yii\widgets\InputWidget;
use yii\helpers\Html;


class GooglePlacesAutoComplete extends InputWidget {

	const API_URL = '//maps.googleapis.com/maps/api/js?';

	public $libraries = 'places';

	public $language = 'en-US';

	public $apiKey = null;

	public $autocompleteOptions = [];

	/**
	 * Renders the widget.
	 */
	public function run()
	{
		$this->registerClientScript();
		if ($this->hasModel()) {
			echo Html::activeTextInput($this->model, $this->attribute, $this->options);
		} else {
			echo Html::textInput($this->name, $this->value, $this->options);
		}
	}

	/**
	 * Registers the needed JavaScript.
	 */
	public function registerClientScript(){
		$elementId = $this->options['id'];
		$scriptOptions = json_encode($this->autocompleteOptions);
		$view = $this->getView();
		$view->registerJsFile(self::API_URL . http_build_query([
				'libraries' => $this->libraries,
				'language' => $this->language,
				'key' => $this->apiKey,
			]));
		$view->registerJs(<<<JS
(function(){
    var input = document.getElementById('{$elementId}');
    var options = {$scriptOptions};
	var ac = new google.maps.places.Autocomplete(input, options);
    ac.addListener('place_changed', function(){
        document.dispatchEvent(new CustomEvent('google_maps_ac_place_changed', {detail: ac.getPlace()}));
    });
})();
JS
		, \yii\web\View::POS_READY);
	}
}

/*
 * Adds undo and redo functionality to the Fusion Page Builder
 */
( function( $ ) {
	var fusionHistoryManager = {},
	    fusionCommands       = new Array( '[]' ),
	    fusionCommandsStates = new Array( '[]' ), // History states
	    maxSteps             = 25, // Maximum steps allowed/saved
	    currStep             = 0; // Current Index of step

	// Is tracking on or off?
	window.tracking = 'on';

	// History state title
	window.fusionHistoryState = '';

	window.fusionHistoryManager = fusionHistoryManager;

	/**
	 * Get editor data and add to array
	 * @param   NULL
	 * @return  NULL
	 */
	fusionHistoryManager.captureEditor = function( ) {

		var allElements;

		if ( fusionHistoryManager.isTrackingOn() ) {

			if ( currStep ==  maxSteps ) { // If reached limit
				fusionCommands.shift(); // Remove first index
			} else {
				currStep += 1; // Else increment index
			}

			if ( currStep > 1 ) {
				$( '.fusion-builder-history-list li' ).removeClass( 'fusion-history-active-state' );
				$( '.fusion-builder-history-list' ).prepend( '<li data-state-id="' + currStep + '" class="history-state-' + currStep + ' fusion-history-active-state"><span class="dashicons dashicons-arrow-right-alt2"></span>' + fusionHistoryState + '</li>' );
			}

			// Get content
			allElements = fusionBuilderGetContent( 'content', true );

			// Add editor data to Array
			fusionCommands[currStep] = allElements;

			// Add history state
			fusionCommandsStates[currStep] = fusionHistoryState;

			// Update buttons
			fusionHistoryManager.updateButtons();
			fusionHistoryState = '';
		}
	};

	/**
	 * Set tracking flag ON.
	 * @param   NULL
	 * @return  NULL
	 */
	fusionHistoryManager.turnOnTracking = function( ) {
		window.tracking = 'on';
	};

	/**
	 * Set tracking flag OFF.
	 * @param   NULL
	 * @return  NULL
	 */
	fusionHistoryManager.turnOffTracking = function( ) {
		window.tracking = 'off';
	};

	/**
	 * Get editor elements of current index for UNDO. Remove all elements currenlty visible in eidor and then reset models
	 * @param   NULL
	 * @return  NULL
	 */
	fusionHistoryManager.doUndo = function( event ) {

		var undoData;

		if ( event ) {
			event.preventDefault();
		}

		// Turn off tracking first, so these actions are not captured
		if ( fusionHistoryManager.hasUndo() ) { // If no data or end of stack and nothing to undo

			fusionHistoryManager.turnOffTracking();

			currStep -= 1;

			// Data to undo
			undoData = fusionCommands[ currStep ];

			if ( '[]' !== undoData ) { // If not empty state

				// Remove all current editor elements first
				FusionPageBuilderApp.clearBuilderLayout();
				FusionPageBuilderApp.$el.find( '.fusion_builder_container' ).remove();

				// Reset models with new elements
				FusionPageBuilderApp.createBuilderLayout( undoData );

				$( '.fusion-builder-history-list li' ).removeClass( 'fusion-history-active-state' );
				$( '.fusion-builder-history-list' ).find( '.history-state-' + currStep ).addClass( 'fusion-history-active-state' );
			}

			// Update buttons
			fusionHistoryManager.updateButtons();
		}
	};

	/**
	 * Get editor elements of current index for REDO. Remove all elements currenlty visible in eidor and then reset models
	 * @param   NULL
	 * @return  NULL
	 */
	fusionHistoryManager.doRedo = function( event ) {

		var redoData;

		if ( event ) {
			event.preventDefault();
		}

		if ( fusionHistoryManager.hasRedo() ) { // If not at end and nothing to redo

			// Turn off tracking, so these actions are not tracked
			fusionHistoryManager.turnOffTracking();

			// Move index
			currStep += 1;

			// Get data to redo
			redoData = fusionCommands[ currStep ];

			// Remove all current editor elements first
			FusionPageBuilderApp.clearBuilderLayout();
			FusionPageBuilderApp.$el.find( '.fusion_builder_container' ).remove();

			// Reset models with new elements
			FusionPageBuilderApp.createBuilderLayout( redoData );

			// Update buttons
			fusionHistoryManager.updateButtons();

			$( '.fusion-builder-history-list li' ).removeClass( 'fusion-history-active-state' );
			$( '.fusion-builder-history-list' ).find( '.history-state-' + currStep ).addClass( 'fusion-history-active-state' );
		}

	};

	/**
	 * Save history state
	 * @param   step
	 * @return  NULL
	 */
	fusionHistoryManager.historyStep = function( step, event ) {

		var stepData;

		if ( event ) {
			event.preventDefault();
		}

		// Get data
		stepData = fusionCommands[step];

		// Remove all current editor elements first
		FusionPageBuilderApp.clearBuilderLayout();
		FusionPageBuilderApp.$el.find( '.fusion_builder_container' ).remove();

		// Reset models with new elements
		FusionPageBuilderApp.createBuilderLayout( stepData );

		currStep = step;

		// Update buttons
		fusionHistoryManager.updateButtons();

		$( '.fusion-builder-history-list li' ).removeClass( 'fusion-history-active-state' );
		$( '.fusion-builder-history-list' ).find( '.history-state-' + currStep ).addClass( 'fusion-history-active-state' );
	};

	/**
	 * Check whether tracking is on or off
	 * @param   NULL
	 * @return  NULL
	 */
	fusionHistoryManager.isTrackingOn = function( ) {
		return 'on' === window.tracking;
	};

	/**
	 * Log current data
	 * @param   NULL
	 * @return  NULL
	 */
	fusionHistoryManager.logStacks = function() {
		console.log( JSON.parse( fusionCommands ) );
	};

	/**
	 * Clear all commands and reset manager
	 * @param   NULL
	 * @return  NULL
	 */
	fusionHistoryManager.clearEditor = function( state ) {

		var allElements;

		fusionCommands       = new Array( '[]' );
		fusionCommandsStates = new Array( '[]' );
		currStep             = 1;
		fusionHistoryState   = '';

		if ( 'blank' === state ) {
			fusionCommands[ currStep ] = '';
		} else {
			allElements = fusionBuilderGetContent( 'content', true );
			fusionCommands[ currStep ] = allElements;
		}

		fusionHistoryManager.updateButtons();

		$( '.fusion-builder-history-list' ).html( '<li data-state-id="1" class="history-state-1 fusion-history-active-state"><span class="dashicons dashicons-arrow-right-alt2"></span>' + fusionBuilderText.empty + '</li>' );
	};

	/**
	 * Check if undo commands exist
	 * @param   NULL
	 * @return  NULL
	 */
	fusionHistoryManager.hasUndo = function() {
		return 1 !== currStep;
	};

	/**
	 * Check if redo commands exist
	 * @param   NULL
	 * @return  NULL
	 */
	fusionHistoryManager.hasRedo = function() {
		return currStep < ( fusionCommands.length - 1 );
	};

	/**
	 * Get existing commands
	 * @param   NULL
	 * @return  {string}	actions
	 */
	fusionHistoryManager.getCommands = function() {
		return fusionCommands;
	};

	/**
	 * Update buttons colors accordingly
	 * @param   NULL
	 * @return  NULL
	 */
	fusionHistoryManager.updateButtons = function() {

		// Undo & History states buttons
		if ( fusionHistoryManager.hasUndo() ) {
			$( '.fusion-builder-layout-buttons-undo' ).addClass( 'fusion-history-has-step' );
			$( '.fusion-builder-layout-buttons-history' ).addClass( 'fusion-history-has-step' );
		} else {
			$( '.fusion-builder-layout-buttons-undo' ).removeClass( 'fusion-history-has-step' );
			$( '.fusion-builder-layout-buttons-history' ).removeClass( 'fusion-history-has-step' );
		}

		// Redo button
		if ( fusionHistoryManager.hasRedo() ) {
			$( '.fusion-builder-layout-buttons-redo' ).addClass( 'fusion-history-has-step' );
		} else {
			$( '.fusion-builder-layout-buttons-redo' ).removeClass( 'fusion-history-has-step' );
		}
	};

})( jQuery );

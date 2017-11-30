var userID = 0;

$('#numInputs, #numOutputs').on('input', function() {
	let numInputs = Number($('#numInputs').val());
	let numOutputs = Number($('#numOutputs').val());
	renderTestView(numInputs, numOutputs);	
});

function renderTestView(numInput, numOutput) {
	$('.testContainer *').remove();
	let source = $('#testField-template').html();
	let template = Handlebars.compile(source);
	for(let i = 0; i < numInput; i++) {
		let context = { title: "Input #" + (i+1), class: "input" };
		let html = template(context);
		$('.testContainer').append(html);
	}
	for(let i = 0; i < numOutput; i++) {
		let context = { title: "Output #" + (i+1), class: "output" };
		let html = template(context);
		$('.testContainer').append(html);
	}
}

$('#addLayer').click(function() {
	let source = $('#layerConfig-template').html();
	$('.layersContainer').append(source);
	$('.removeLayer').off('click');
	$('.removeLayer').on('click', function() {
		$(this).closest('.layer').remove();
	});
});

$('#fileUpload').change(function() {
	var fileData = this.files[0];
	var formData = new FormData();
	formData.append('file', fileData);

	$.ajax(
	{
		type: 'POST',
		url: 'upload.php',
		data: formData,
		processData: false,
		contentType: false,
		cache: false
	}).done(function(resp) {
		let result = JSON.parse(resp);
		if(result.success) {
			userID = JSON.parse(resp).id;
		}
		else {
			alert(result.error);
		}
	});
});

$('#train').click(function() {
	let inputs = $('#numInputs').val();
	let outputs = $('#numOutputs').val();
	let epochLimit = $('#epochLimit').val();
	let targetError = $('#targetError').val();
	
	let neuronCounts = $('.neuronInput').toArray().map(function(x) { return Number(x.value); });
	let activationMethods = $('.activationInput').toArray().map(function(x) { return x.value; });
	
	$.ajax(
	{
		type: 'POST',
		url: 'train.php',
		data: {
			userID: userID,
			inputs: inputs,
			outputs, outputs,
			epochLimit: epochLimit,
			targetError: targetError,
			neuronCounts: neuronCounts,
			activationMethods: activationMethods
		}
	}).done(function(resp) {
		let results = JSON.parse(resp);
		if(results.success) {
			alert("Training successful");
		}
		else {
			alert(results.error);
		}
	});
});

$('#test').click(function() {
	let inputs = $('.testContainer .input').toArray().map(function(x) { return Number(x.value); });
	$.ajax(
	{
		type: 'POST',
		url: 'test.php',
		data: {
			userID: userID,
			inputs: inputs
		}
	}).done(function(resp) {
		let outputFields = $('.testContainer .output');
		let result = JSON.parse(resp);
		if(result.success) {
			for(let i = 0; i < result.output.length; i++) {
				outputFields.eq(i).val(result.output[i]);
			}
		}
		else {
			aler(result.error);
		}
	});
});


function MoJWTdivVisibility( divId, allMethodDivID ) {
	
	MoJWThideVisibility( allMethodDivID );
	div = document.getElementById( divId + "_div" );
	div.style.display = "block";
}

function MoJWThideVisibility ( allMethodDivID ) {
	var MethodsDivArray = allMethodDivID.split(",");
	MethodsDivArray.forEach(element => {
		div = document.getElementById( element + "_div" );
		if( div )
			div.style.display = "none";
	});
}


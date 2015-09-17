pf.storage = {
	"get": function(name, def){
		var item = window.localStorage.getItem(name);

		if(item === null){
			pf.storage.set(name, def);
			return def;
		}

		return item;
	},

	"set": function(name, data){
		window.localStorage.setItem(name, data);
	}
};
function readMatrix(file_path)
	local file = io.open(file_path, "r");
	local mat = {};
	n = file:read("*num");
	for i=1,n do
		mat[i] = {};
		for j=1,n do
			mat[i][j] = file:read("*num");
		end
	end
	
	return mat;
end
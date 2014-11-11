\def\readmatrix#1{%
    \directlua{
    	matrix = require 'matrix';
    	m = readMatrix(#1);
    	tex.sprint(matrix.latex(m,c));
    }
}
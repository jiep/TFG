/*Funcion que devuelve la posicion que ocupa un equipo en el ranking que se le pasa como parametro*/

private static int devolver (ArrayList<String> a, String s){
       int n = 0;
       for (int i = 0; i < MAX ;i++){
           if(a.get(i).equals(s))
               n = i;
       }
       return n;
}
public class TauKendall{    
  public static void main(String[] args) {
    int v11, v12, v21, v22;
    int c = 0;
    int d = 0;
    int i = 0;
    int j = 0;
        
    while (i<MAX-1){
       String eq1 = equipos.get(i);
       j = i+1;
       while(j<MAX){
          String eq2 = equipos.get(j);
          v11 = devolver(r1,eq1);
          v21 = devolver(r1,eq2);
          v12 = devolver(r2,eq1);
          v22 = devolver(r2,eq2);
          if(((v11>v21)&&(v12>v22))||((v11<v21)&&(v12<v22))){c++;}
          else{d++;}
          j++;
       }
       i++;
    }
    System.out.println((double)(c-d)/(double)(MAX*(MAX-1)/2));        
  }   
}

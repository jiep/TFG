public class RhoSpearman{

  public static void main(String[] args) {
        
    int i = 0;
    double pos1,pos2;
    double total = 0;

    while (i<MAX){
       String equipo = equipos.get(i);
       pos1 = devolver(r1,equipo)+1;
       pos2 = devolver(r2,equipo)+1;	
       //total = total + Math.abs(pos1-pos2);
       total = total + (Math.abs(pos1-pos2)/(Math.min(pos1,pos2)));
       i++;
    }
        
    System.out.println(total);
        
  } 
}

function outputSummary(array, name, areatoChange)
{
    array.forEach(element => {
      if(element.title == name)
      {
        areatoChange.innerHTML = "<h2>"+element.title+"</h2>";
        areatoChange.innerHTML += "<img src=\"../images/"+element.image+"\">";
        areatoChange.innerHTML += "<p>"+element.year, element.length+"</p>\n";
        areatoChange.innerHTML += "<p>"+element.genre+"</p>\n";
        areatoChange.innerHTML += "<p>"+element.director+"</p>\n";
        areatoChange.innerHTML += "<p>"+element.score+"</p>\n";
        areatoChange.innerHTML += "<p>"+element.description+"</p>\n";
      }
    });
}
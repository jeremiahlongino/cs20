import { useState } from "react";
import "./App.css";

const MyColors = [
  { value: 1, color: "#000000" },
  { value: 2, color: "#f1c40f" },
  { value: 3, color: "#9b59b6" },
  { value: 4, color: "#3498db" },
  { value: 5, color: "#9b59b6" },
  { value: 6, color: "#f1c40f" },
  { value: 7, color: "#FF0000" },
  { value: 8, color: "#3498db" },
  { value: 9, color: "#000000" },
];

function Block({ color, children }) {
  const [hovered, setHovered] = useState(false);


  // Have to change color of text to black and background color to white
  return (
    <div
      className="block"
      style={{
        backgroundColor: hovered ? "white" : color,
        color: hovered ? "black" : "white",
      }}
      onMouseEnter={() => setHovered(true)}
      onMouseLeave={() => setHovered(false)}
      onClick={() => alert(children)}
    >
      {children}
    </div>
  );
}

function ColorGrid({ colorArray }) {
  return (
    <div className="color-grid">
      {colorArray.map((item) => (
        <Block key={item.value} color={item.color}>
          {item.value}
        </Block>
      ))}
    </div>
  );
}

function App() {
  return (
    <div className="app">
      <h1 className="h1">Color Grid</h1>
      <ColorGrid colorArray={MyColors} />
    </div>
  );
}

export default App;

import { BrowserRouter, Routes, Route, Link } from "react-router-dom";
import Register from "./pages/Register";
import Login from "./pages/Login";
import Posts from "./pages/Posts";
import { useAuth } from "./AuthContext";
import "./styles.css";

function Nav() {
  const { user } = useAuth();
  return (
    <nav className="nav">
      <Link to="/">Posts</Link>
      <div className="spacer" />
      <Link to="/register">Register</Link>
      <Link to="/login">{user ? "Re-login" : "Login"}</Link>
    </nav>
  );
}

export default function App() {
  return (
    <BrowserRouter>
      <Nav />
      <div className="container">
        <Routes>
          <Route path="/" element={<Posts />} />
          <Route path="/register" element={<Register />} />
          <Route path="/login" element={<Login />} />
        </Routes>
      </div>
    </BrowserRouter>
  );
}

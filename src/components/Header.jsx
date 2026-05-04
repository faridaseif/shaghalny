import React from 'react';
import './Header.css';
import { FaHome, FaMapMarkedAlt, FaComments, FaSuitcase, FaUserCircle, FaPlusCircle, FaLifeRing } from 'react-icons/fa';

const navItems = [
  { label: 'Home', icon: <FaHome /> },
  { label: 'Jobs Map', icon: <FaMapMarkedAlt /> },
  { label: 'Social Feed', icon: <FaComments /> },
  { label: 'Messages', icon: <FaComments /> },
  { label: 'My Jobs', icon: <FaSuitcase /> },
  { label: 'My Profile', icon: <FaUserCircle /> },
  { label: 'Post Job', icon: <FaPlusCircle /> },
  { label: 'Support', icon: <FaLifeRing /> }
];

function Header({ username = "Alex" }) {
  return (
    <header className="header">
      <div className="header-left">
        <div className="header-logo">
          <span role="img" aria-label="app-icon" className="logo-icon">🏢</span>
          <span className="logo-text">TeenWork</span>
        </div>
        <nav>
          <ul className="header-nav">
            {navItems.map((item) => (
              <li key={item.label} className="nav-item">
                <span className="nav-icon">{item.icon}</span>
                <span>{item.label}</span>
              </li>
            ))}
          </ul>
        </nav>
      </div>
      <div className="header-right">
        <span className="header-greeting">
          Welcome, <b>{username}</b> <span role="img" aria-label="waving">👋</span>
        </span>
        <div className="header-avatar">
          {username[0].toUpperCase()}
        </div>
      </div>
    </header>
  );
}

export default Header;
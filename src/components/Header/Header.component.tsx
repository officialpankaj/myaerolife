import { NavLink, useNavigate } from "react-router-dom";
import { Routes } from "../../constants/routes.constants";

import logo from "../../assets/logo.png";
import { useDispatch, useSelector } from "react-redux";
import {
  setAccessToken,
  setUserDetails,
  updateIsAuthenticated,
} from "../../store/reducers/commonSlice";
import type { RootState } from "../../store/Store";
import { useState, useRef, useEffect } from "react";
import { UserRole } from "../../constants/common.constants";

const Header = () => {
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const { userDetails } = useSelector((state: RootState) => state.common);
  const [menuOpen, setMenuOpen] = useState(false);
  const menuRef = useRef<HTMLDivElement>(null);

  const handleLogout = () => {
    localStorage.clear();
    navigate(Routes.LOGIN, { state: { role: userDetails?.role } });
    dispatch(updateIsAuthenticated(false));
    dispatch(setUserDetails({}));
    dispatch(setAccessToken(null));
    setMenuOpen(false);
  };

  // Close menu on outside click
  useEffect(() => {
    function handleClickOutside(event: MouseEvent) {
      if (menuRef.current && !menuRef.current.contains(event.target as Node)) {
        setMenuOpen(false);
      }
    }
    if (menuOpen) {
      document.addEventListener("mousedown", handleClickOutside);
    } else {
      document.removeEventListener("mousedown", handleClickOutside);
    }
    return () => {
      document.removeEventListener("mousedown", handleClickOutside);
    };
  }, [menuOpen]);

  return (
    <header className="sticky top-0 left-0 z-50 w-full bg-white shadow-lg">
      <div className="flex w-full items-center justify-between px-6 py-2">
        <NavLink to="/" className="flex items-center gap-3">
          <img
            src={logo}
            className="h-12 rounded-full transition-transform duration-200 hover:scale-105"
            alt="Logo"
          />
        </NavLink>
        {/* Hamburger menu only for non-logged-in users */}
        {!userDetails?.id ? (
          <div className="relative" ref={menuRef}>
            <button
              className="flex h-10 w-10 flex-col items-center justify-center rounded-lg border border-gray-200 bg-white shadow hover:bg-gray-100 focus:outline-none md:h-12 md:w-12"
              aria-label="Open menu"
              onClick={() => setMenuOpen((open) => !open)}
            >
              <span
                className={`bg-primaryv2 block h-0.5 w-6 rounded transition-all duration-200 ${menuOpen ? "-translate-x-0.5 translate-y-2 rotate-45" : ""}`}
              ></span>
              <span
                className={`bg-primaryv2 my-1 block h-0.5 w-6 rounded transition-all duration-200 ${menuOpen ? "opacity-0" : ""}`}
              ></span>
              <span
                className={`bg-primaryv2 block h-0.5 w-6 rounded transition-all duration-200 ${menuOpen ? "-translate-x-[3px] -translate-y-1 -rotate-45" : ""}`}
              ></span>
            </button>
            {/* Dropdown menu */}
            {menuOpen && (
              <div className="absolute right-0 z-50 mt-2 flex w-48 flex-col rounded-xl border border-gray-100 bg-white p-2 shadow-lg">
                <button
                  className="text-primaryv2 hover:bg-primaryv2 cursor-pointer rounded-lg px-5 py-2 font-medium transition-colors duration-150 hover:text-white"
                  onClick={() => {
                    setMenuOpen(false);
                    navigate(Routes.LOGIN, { state: { role: UserRole.HERO } });
                  }}
                >
                  Hero Login
                </button>
                <button
                  className="text-primaryv2 hover:bg-primaryv2 cursor-pointer rounded-lg px-5 py-2 font-medium transition-colors duration-150 hover:text-white"
                  onClick={() => {
                    setMenuOpen(false);
                    navigate(Routes.LOGIN, {
                      state: { role: UserRole.MANAGER },
                    });
                  }}
                >
                  Manager Login
                </button>
              </div>
            )}
          </div>
        ) : (
          <button
            onClick={handleLogout}
            className="bg-primaryv2 hover:bg-primaryv7 rounded-lg px-4 py-2 font-semibold text-white shadow transition-colors duration-150"
          >
            Logout
          </button>
        )}
      </div>
    </header>
  );
};

export default Header;

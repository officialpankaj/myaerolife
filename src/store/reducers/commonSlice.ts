import { createSlice } from "@reduxjs/toolkit";

interface IUserDetails {
  id: string;
  employee_code: string;
  role: string;
  password: string;
  active: string;
  state: string;
  zone: string;
  region: string;
  hq: string;
  chairname: string;
  created_at: string;
  updated_at: string;
}

interface CommonState {
  userDetails: Partial<IUserDetails>;
  accesstoken: string | null;
  isAuthenticated: boolean;
  mobileView: boolean;
}

const initialState: CommonState = {
  userDetails: {},
  accesstoken: null,
  isAuthenticated: false,
  mobileView: false,
};

const commonSlice = createSlice({
  name: "common",
  initialState: initialState,
  reducers: {
    setUserDetails: (state, action) => {
      state.userDetails = action.payload;
    },
    setAccessToken: (state, action) => {
      state.accesstoken = action.payload;
    },
    updateIsAuthenticated: (state, action) => {
      state.isAuthenticated = action.payload;
    },
    setMobileView: (state, action) => {
      state.mobileView = action.payload;
    },
  },
});

export const {
  setUserDetails,
  setAccessToken,
  updateIsAuthenticated,
  setMobileView,
} = commonSlice.actions;

export default commonSlice.reducer;

import { configureStore } from "@reduxjs/toolkit";
import { combineReducers } from "@reduxjs/toolkit";
import { persistReducer, persistStore } from "redux-persist";
import storage from "redux-persist/lib/storage";

import commonSlice from "./reducers/commonSlice";

const rootReducer = combineReducers({
  common: commonSlice,
});

const persistConfig = {
  key: "myaerolife",
  version: 1,
  storage,
};

const persistedReducer = persistReducer(persistConfig, rootReducer);

const store = configureStore({
  reducer: persistedReducer,
  middleware: (getDefaultMiddleware) =>
    getDefaultMiddleware({
      serializableCheck: {
        ignoredActions: ["persist/REHYDRATE"],
      },
    }),
});

let persistor = persistStore(store);

export type RootState = ReturnType<typeof store.getState>;
export type AppDispatch = typeof store.dispatch;

export { store, persistor };

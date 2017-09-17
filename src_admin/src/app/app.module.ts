import { NgModule, ApplicationRef } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { HttpModule } from '@angular/http';
import { RouterModule } from '@angular/router';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { TranslateService } from '@ngx-translate/core';

import { BusyModule, BusyConfig } from 'angular2-busy';
import { PaginationModule } from 'ng2-bootstrap';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
/*
 * Platform and Environment providers/directives/pipes
 */
import { routing } from './app.routing';

// App is our top level component
import { App } from './app.component';
import { AppState, InternalStateType } from './app.service';
import { GlobalState } from './global.state';
import { NgaModule } from './theme/nga.module';
import { PagesModule } from './pages/pages.module';

import { UtilsModule, loaderMessage, loaderTemplate } from './utils/index';
import { ServerApiModule } from './services/serverApi/index';

// Application wide providers
const APP_PROVIDERS = [
  AppState,
  GlobalState
];

export type StoreType = {
  state: InternalStateType,
  restoreInputValues: () => void,
  disposeOldHosts: () => void
};

@NgModule({
  bootstrap: [App],
  declarations: [
    App
  ],
  imports: [
    BrowserModule,
    HttpModule,
    RouterModule,
    FormsModule,
    ReactiveFormsModule,
    BrowserAnimationsModule,
    NgaModule.forRoot(),
    NgbModule.forRoot(),
    PaginationModule.forRoot(),
    BusyModule.forRoot(new BusyConfig({ message: loaderMessage, template: loaderTemplate })),
    PagesModule,
    UtilsModule,
    ServerApiModule,
    routing
  ],
  providers: [
    APP_PROVIDERS
  ]
})

export class AppModule {
  constructor(public appState: AppState) { }
}

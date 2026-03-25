import { Component, type ErrorInfo, type ReactNode } from "react";

interface Props {
  children: ReactNode;
  fallback?: ReactNode;
}

interface State {
  hasError: boolean;
  error: Error | null;
}

export class ErrorBoundary extends Component<Props, State> {
  state: State = { hasError: false, error: null };

  static getDerivedStateFromError(error: Error): State {
    return { hasError: true, error };
  }

  componentDidCatch(error: Error, errorInfo: ErrorInfo) {
    console.error("App error:", error, errorInfo);
  }

  render() {
    if (this.state.hasError && this.state.error) {
      if (this.props.fallback) return this.props.fallback;
      return (
        <div className="min-h-screen bg-background flex items-center justify-center p-6">
          <div className="max-w-md w-full bg-card border border-border rounded-2xl p-8 shadow-card">
            <h1 className="text-xl font-display font-bold text-foreground mb-2">
              Something went wrong
            </h1>
            <p className="text-sm text-muted-foreground mb-4">
              {this.state.error.message}
            </p>
            <button
              type="button"
              onClick={() => this.setState({ hasError: false, error: null })}
              className="px-4 py-2 rounded-lg bg-primary text-primary-foreground font-medium hover:opacity-90"
            >
              Try again
            </button>
          </div>
        </div>
      );
    }
    return this.props.children;
  }
}

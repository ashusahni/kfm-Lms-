import { useState } from "react";
import { Link, useNavigate, useLocation, useSearchParams } from "react-router-dom";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { useAuth } from "@/context/AuthContext";
import { getAuthRole } from "@/lib/api";
import { useConfig } from "@/context/ConfigContext";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { Label } from "@/components/ui/label";
import { Alert, AlertDescription } from "@/components/ui/alert";
import { AnimatedBackground } from "@/components/aceternity/AnimatedBackground";
import { motion } from "framer-motion";
import { cn } from "@/lib/utils";

const emailSchema = z.object({
  username: z.string().min(1, "Email is required").email("Invalid email"),
  password: z.string().min(6, "At least 6 characters"),
});

const mobileSchema = z.object({
  country_code: z.string().min(1, "Country code is required"),
  mobile: z.string().min(1, "Mobile number is required").regex(/^[0-9]+$/, "Numbers only"),
  password: z.string().min(6, "At least 6 characters"),
});

type EmailFormData = z.infer<typeof emailSchema>;
type MobileFormData = z.infer<typeof mobileSchema>;

type LoginMode = "email" | "mobile";

const Login = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const [searchParams] = useSearchParams();
  const verified = searchParams.get("verified") === "1";
  const { login, loading } = useAuth();
  const { appConfig } = useConfig();
  const [error, setError] = useState<string | null>(null);
  const defaultMode: LoginMode =
    appConfig?.register_method === "mobile" ? "mobile" : "email";
  const [mode, setMode] = useState<LoginMode>(defaultMode);

  const emailForm = useForm<EmailFormData>({
    resolver: zodResolver(emailSchema),
    defaultValues: { username: "", password: "" },
  });

  const mobileForm = useForm<MobileFormData>({
    resolver: zodResolver(mobileSchema),
    defaultValues: { country_code: "+91", mobile: "", password: "" },
  });

  const onEmailSubmit = async (data: EmailFormData) => {
    setError(null);
    const result = await login({
      username: data.username.trim(),
      password: data.password,
    });
    handleLoginResult(result);
  };

  const onMobileSubmit = async (data: MobileFormData) => {
    setError(null);
    const countryCode = data.country_code.replace(/\s/g, "").startsWith("+")
      ? data.country_code.trim()
      : `+${data.country_code.trim()}`;
    const result = await login({
      mobile: data.mobile.replace(/\D/g, ""),
      country_code: countryCode,
      password: data.password,
    });
    handleLoginResult(result);
  };

  const handleLoginResult = (result: { ok: boolean; message?: string }) => {
    if (result.ok) {
      const from = (location.state as { from?: { pathname?: string } })?.from?.pathname;
      const role = getAuthRole();
      const isInstructor = role === "teacher" || role === "organization";
      if (from && (isInstructor ? from.startsWith("/dietician") : from.startsWith("/panel"))) {
        navigate(from, { replace: true });
      } else {
        navigate(isInstructor ? "/dietician" : "/panel", { replace: true });
      }
    } else {
      setError(result.message ?? "Login failed");
    }
  };

  return (
    <AnimatedBackground variant="grid">
      <motion.div
        initial={{ opacity: 0, y: 12 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.4 }}
        className="w-full max-w-md px-4"
      >
        <Card className="w-full border-border/80 bg-card/90 shadow-elevated backdrop-blur-md">
          <CardHeader className="space-y-1 text-center">
            <CardTitle className="text-2xl font-display">Sign in</CardTitle>
            <CardDescription>
              Enter your email or mobile number and password
            </CardDescription>
          </CardHeader>
          <CardContent>
            {verified && (
              <Alert className="mb-4 border-green-500/50 bg-green-500/10">
                <AlertDescription>
                  Email verified. You can sign in now.
                </AlertDescription>
              </Alert>
            )}

            <div className="flex rounded-lg border border-border bg-muted/50 p-1 mb-4">
              <button
                type="button"
                onClick={() => setMode("email")}
                className={cn(
                  "flex-1 rounded-md py-2 text-sm font-medium transition-colors",
                  mode === "email"
                    ? "bg-background text-foreground shadow-sm"
                    : "text-muted-foreground hover:text-foreground"
                )}
              >
                Email
              </button>
              <button
                type="button"
                onClick={() => setMode("mobile")}
                className={cn(
                  "flex-1 rounded-md py-2 text-sm font-medium transition-colors",
                  mode === "mobile"
                    ? "bg-background text-foreground shadow-sm"
                    : "text-muted-foreground hover:text-foreground"
                )}
              >
                Mobile
              </button>
            </div>

            {mode === "email" ? (
              <form onSubmit={emailForm.handleSubmit(onEmailSubmit)} className="space-y-4">
                {error && (
                  <Alert variant="destructive">
                    <AlertDescription>{error}</AlertDescription>
                  </Alert>
                )}
                <div className="space-y-2">
                  <Label htmlFor="username">Email</Label>
                  <Input
                    id="username"
                    type="email"
                    placeholder="you@example.com"
                    autoComplete="email"
                    className="transition-shadow focus-visible:ring-2"
                    {...emailForm.register("username")}
                  />
                  {emailForm.formState.errors.username && (
                    <p className="text-sm text-destructive">
                      {emailForm.formState.errors.username.message}
                    </p>
                  )}
                </div>
                <div className="space-y-2">
                  <Label htmlFor="password">Password</Label>
                  <Input
                    id="password"
                    type="password"
                    autoComplete="current-password"
                    className="transition-shadow focus-visible:ring-2"
                    {...emailForm.register("password")}
                  />
                  {emailForm.formState.errors.password && (
                    <p className="text-sm text-destructive">
                      {emailForm.formState.errors.password.message}
                    </p>
                  )}
                  <p className="text-right">
                    <Link to="/forgot-password" className="text-sm text-primary hover:underline">
                      Forgot password?
                    </Link>
                  </p>
                </div>
                <Button
                  type="submit"
                  className="w-full bg-gradient-cta text-primary-foreground shadow-md hover:opacity-90 transition-opacity"
                  disabled={loading}
                >
                  {loading ? "Signing in…" : "Sign in"}
                </Button>
              </form>
            ) : (
              <form onSubmit={mobileForm.handleSubmit(onMobileSubmit)} className="space-y-4">
                {error && (
                  <Alert variant="destructive">
                    <AlertDescription>{error}</AlertDescription>
                  </Alert>
                )}
                <div className="grid grid-cols-[1fr_2fr] gap-2">
                  <div className="space-y-2">
                    <Label htmlFor="country_code">Country code</Label>
                    <Input
                      id="country_code"
                      type="text"
                      placeholder="+91"
                      autoComplete="tel-country-code"
                      className="transition-shadow focus-visible:ring-2"
                      {...mobileForm.register("country_code")}
                    />
                    {mobileForm.formState.errors.country_code && (
                      <p className="text-sm text-destructive">
                        {mobileForm.formState.errors.country_code.message}
                      </p>
                    )}
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="mobile">Mobile number</Label>
                    <Input
                      id="mobile"
                      type="tel"
                      placeholder="9876543210"
                      autoComplete="tel-national"
                      className="transition-shadow focus-visible:ring-2"
                      {...mobileForm.register("mobile")}
                    />
                    {mobileForm.formState.errors.mobile && (
                      <p className="text-sm text-destructive">
                        {mobileForm.formState.errors.mobile.message}
                      </p>
                    )}
                  </div>
                </div>
                <div className="space-y-2">
                  <Label htmlFor="mobile-password">Password</Label>
                  <Input
                    id="mobile-password"
                    type="password"
                    autoComplete="current-password"
                    className="transition-shadow focus-visible:ring-2"
                    {...mobileForm.register("password")}
                  />
                  {mobileForm.formState.errors.password && (
                    <p className="text-sm text-destructive">
                      {mobileForm.formState.errors.password.message}
                    </p>
                  )}
                  <p className="text-right">
                    <Link to="/forgot-password" className="text-sm text-primary hover:underline">
                      Forgot password?
                    </Link>
                  </p>
                </div>
                <Button
                  type="submit"
                  className="w-full bg-gradient-cta text-primary-foreground shadow-md hover:opacity-90 transition-opacity"
                  disabled={loading}
                >
                  {loading ? "Signing in…" : "Sign in"}
                </Button>
              </form>
            )}

            <p className="mt-4 text-center text-sm text-muted-foreground">
              Don&apos;t have an account?{" "}
              <Link to="/register" className="text-primary font-medium hover:underline">
                Register
              </Link>
            </p>
          </CardContent>
        </Card>
      </motion.div>
    </AnimatedBackground>
  );
};

export default Login;
